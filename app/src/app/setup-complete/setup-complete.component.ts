import { Component, OnInit } from '@angular/core';
import {loadStripe} from "@stripe/stripe-js";
import {environment} from "../../environments/environment";
import {Router} from "@angular/router";
import {ToastrService} from "ngx-toastr";

@Component({
  selector: 'app-setup-complete',
  templateUrl: './setup-complete.component.html',
  styleUrls: ['./setup-complete.component.scss']
})
export class SetupCompleteComponent implements OnInit {

  stripe: any;
  message: string = '';

  constructor(
    private router: Router,
    private toastR: ToastrService,
  ) { }

  async ngOnInit() {
    // Initialize Stripe.js using your publishable key
    this.stripe = await loadStripe(environment.stripe);


    // Retrieve the "setup_intent_client_secret" query parameter appended to
    // your return_url by Stripe.js
    const clientSecret = new URLSearchParams(window.location.search).get(
      'setup_intent_client_secret'
    );

    // Retrieve the SetupIntent
    this.stripe.retrieveSetupIntent(clientSecret).then((setupIntent: any) => {
      // @ts-ignore
      console.log( setupIntent.setupIntent);
      // Inspect the SetupIntent `status` to indicate the status of the payment
      // to your customer.
      //
      // Some payment methods will [immediately succeed or fail][0] upon
      // confirmation, while others will first enter a `processing` state.
      //
      // [0]: https://stripe.com/docs/payments/payment-methods#payment-notification
      switch (setupIntent.setupIntent.status) {
        case 'succeeded': {
          this.message = 'Success! Your payment method has been saved.';
          this.toastR.success(this.message);
          this.router.navigate(['card']);
          break;
        }

        case 'processing': {
          this.message = "Processing payment details. We'll update you when processing is complete.";
          break;
        }

        case 'requires_payment_method': {
          this.message = 'Failed to process payment details. Please try another payment method.';

          // Redirect your user back to your payment page to attempt collecting
          // payment again
          this.router.navigate(['card']);

          break;
        }
      }
    });
  }

}
