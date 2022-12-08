import {AfterViewChecked, Component, OnInit} from '@angular/core';
import {loadStripe} from '@stripe/stripe-js';
import {environment} from "../../environments/environment";
import {HttpClient} from "@angular/common/http";
import {SubscribeComponent} from "../../lib/component/subscribe/subscribe.component";
import {ActivatedRoute, Router} from "@angular/router";
import {ToastrService} from "ngx-toastr";

@Component({
  selector: 'app-card',
  templateUrl: './card.component.html',
  styleUrls: ['./card.component.scss']
})
export class CardComponent extends SubscribeComponent implements OnInit {

  stripe: any = null;
  cards: any[] = [];
  redirect: string = '';
  constructor(
    private http: HttpClient,
    private router: Router,
    private route: ActivatedRoute,
    private toastR: ToastrService,
  ) {
    super();
  }

  detach(index: number) {
    console.log(index);
    this.http.delete('api/available/card/' + this.cards[index].id)
      .subscribe((data:any) => {
        this.loadCards();
      })
  }

  loadCards() {
    this.add(this.http.get('api/available/card').subscribe((data: any) => {
      this.cards = data;
    }))
  }

  async ngOnInit() {

    this.stripe = await loadStripe(environment.stripe);




    this.add(this.route.queryParams.subscribe((param: any) => {
      if(param.setup_intent_client_secret) {
        const clientSecret = param.setup_intent_client_secret;
        this.stripe.retrieveSetupIntent(clientSecret).then((setupIntent: any) => {
          // @ts-ignore
          // Inspect the SetupIntent `status` to indicate the status of the payment
          // to your customer.
          //
          // Some payment methods will [immediately succeed or fail][0] upon
          // confirmation, while others will first enter a `processing` state.
          //
          // [0]: https://stripe.com/docs/payments/payment-methods#payment-notification
          switch (setupIntent.setupIntent.status) {
            case 'succeeded': {
              let message = 'Success! Your payment method has been saved.';
              this.toastR.success(message);
              console.log(this.redirect);
              if(param.redirect) this.router.navigate([param.redirect]);

              break;
            }

            case 'processing': {
              let message = "Processing payment details. We'll update you when processing is complete.";
              this.toastR.success(message);
              if(param.redirect) this.router.navigate([param.redirect]);
              break;
            }

            case 'requires_payment_method': {
              let message = 'Failed to process payment details. Please try another payment method.';
              this.toastR.error(message);
              // Redirect your user back to your payment page to attempt collecting
              // payment again
              this.router.navigate(['card']);

              break;
            }
          }
        });
      }

      if(param.redirect) {
        this.redirect = param.redirect;
      }
    }))
    const clientSecret = new URLSearchParams(window.location.search).get(
      'setup_intent_client_secret'
    );

    // Retrieve the SetupIntent

    this.loadCards();
    this.add(
      this.http.post('api/set-up-intent', {}).subscribe((data: any) => {
        let options = {
          clientSecret: data.clientSecret,
        }
        const elements = this.stripe.elements(options);
        const style = {
          base: {
            color: '#32325d',
            fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
            fontSmoothing: 'antialiased',
            fontSize: '16px',
            '::placeholder': {
              color: '#aab7c4'
            },
          },
          invalid: {
            color: '#fa755a',
            iconColor: '#fa755a'
          }
        };
        let card = elements.create('payment');
        card.mount('#payment-element');

        const form: any = document.getElementById('payment-form');
        form.addEventListener('submit', async (event: any) => {
          event.preventDefault();

          const {error} = await this.stripe.confirmSetup({
            //`Elements` instance that was used to create the Payment Element
            elements,
            confirmParams: {
              return_url: environment.local + 'card?redirect=' + this.redirect,
            }
          });

          if (error) {
            // This point will only be reached if there is an immediate error when
            // confirming the payment. Show error to your customer (for example, payment
            // details incomplete)
            const messageContainer: any = document.querySelector('#error-message');
            messageContainer.textContent = error.message;
          } else {
            // Your customer will be redirected to your `return_url`. For some payment
            // methods like iDEAL, your customer will be redirected to an intermediate
            // site first to authorize the payment, then redirected to the `return_url`.
          }
        });
      })
    );
  }
}
