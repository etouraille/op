import {AfterViewChecked, Component, OnInit} from '@angular/core';
import {loadStripe} from '@stripe/stripe-js';
import {environment} from "../../environments/environment";
import {HttpClient} from "@angular/common/http";
import {SubscribeComponent} from "../../lib/component/subscribe/subscribe.component";

@Component({
  selector: 'app-card',
  templateUrl: './card.component.html',
  styleUrls: ['./card.component.scss']
})
export class CardComponent extends SubscribeComponent implements OnInit {

  stripe: any = null;
  cards: any[] = [];
  constructor(
    private http: HttpClient,
  ) {
    super();
  }

  detach(index: number) {
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
            }
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
              return_url: environment.local + 'setup-complete',
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
