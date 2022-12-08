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

  }

}
