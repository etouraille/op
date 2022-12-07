<?php


namespace App\State;

use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\State\ProcessorInterface;
use App\Dto\PayReturn;
use App\Entity\Compensation;
use App\Entity\Expense;
use App\Entity\Income;
use App\Entity\Reservation;
use App\Entity\User;
use App\Service\StripePaymentMethod;
use Doctrine\ORM\EntityManagerInterface;
use http\Exception\UnexpectedValueException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Security\Core\Security;

class CompensationStateProcessor implements ProcessorInterface
{

    private $secret;

    public function __construct(
        private EntityManagerInterface $em,
        private StripePaymentMethod $methods,
        $stripe,
    ) {
        $this->secret = $stripe;
        \Stripe\Stripe::setApiKey($stripe);
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): PayReturn | Compensation
    {
        if ($data instanceof Compensation && $operation instanceof Post) {
            $user = $data->getUser();
            $thing = $data->getThing();
            $rate = $data->getRate();
            if ($rate<0 || $rate > 100) {
                throw new UnexpectedValueException('Taux incompatible');
            }
            $charge = round($thing->getPrice() * $rate);

            $paymentIds = $this->methods->get($user);

            if(count($paymentIds) === 0) {
                return new PayReturn(false, null, null, 'Pas de moyens de paiement disponibles');
            } else {
                $index = 0;
                $success = false;
                do {
                    $success = $this->payCharge($charge, $paymentIds[$index], $user);
                    $index ++;
                } while(!$success && $index < count($paymentIds));

                if(!$success) {
                    return new PayReturn(false, null, null, 'Echec du paiement');
                }

                if($success) {

                    $income = new Income();
                    $income->setUser($thing->getOwner());
                    $income->setAmount($charge);
                    // $income->setExpense(null);
                    $income->setThing($thing);
                    $income->setStatus('pending');
                    $this->em->persist($income);
                    $this->em->flush();

                }

            }

            if($success) {
                $this->em->persist($data);
                $this->em->flush();
            }

            return $data;
        }
    }

    public function payCharge($charge, $paymentMethodId, User $user) {
        try {
            $intent = \Stripe\PaymentIntent::create([
                'amount' => $charge,
                'currency' => 'eur',
                'customer' => $user->getStripeCustomerId(),
                'payment_method' => $paymentMethodId,
                'off_session' => true,
                'confirm' => true,
            ]);
            return true;
        } catch(\Exception $e) {
            return false;
        }
    }
}