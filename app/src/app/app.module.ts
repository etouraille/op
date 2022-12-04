import { NgModule } from '@angular/core';
import { BrowserModule } from '@angular/platform-browser';

import { AppRoutingModule } from './app-routing.module';
import { AppComponent } from './app.component';
import { HomeComponent } from './home/home.component';
import {HeaderComponent} from "../lib/component/header/header.component";
import { LoginComponent } from './login/login.component';
import { SubscribeComponent } from './subscribe/subscribe.component';
import { SubscribeComponent as UnsubscribeComponent } from './../lib/component/subscribe/subscribe.component';
import {FormsModule, ReactiveFormsModule} from "@angular/forms";
import {HTTP_INTERCEPTORS, HttpClientModule} from "@angular/common/http";
import {AuthInterceptor} from "../lib/injector/injector";
import { StoreModule } from '@ngrx/store';
import {loginReducer} from "../lib/reducers/app-reducer";
import { ThingsComponent } from './things/things.component';
import {NgbModalModule, NgbModule} from '@ng-bootstrap/ng-bootstrap';
import {CommonModule} from "@angular/common";
import {CalendarComponent} from "../lib/component/calendar/calendar.component";
import { CardComponent } from './card/card.component';
import { SetupCompleteComponent } from './setup-complete/setup-complete.component';
import { IncomeComponent } from './income/income.component';
import {PaymentCardComponent} from "../lib/component/payment-card/payment-card.component";
import { CoinComponent } from './coin/coin.component';
import {LoggedGuard} from "../lib/guard/logged.guard";
import {OverlayContainer, ToastrModule} from "ngx-toastr";
import {CentPipe} from "../lib/pipe/cent.pipe";
import { AddComponent } from './add/add.component';
import {PicturesComponent} from "../lib/component/pictures/pictures.component";
import {FileUploadModule} from "../lib/module/file-upload/file-upload.module";
import {PictureComponent} from "../lib/component/picture/picture.component";
import {BrowserAnimationsModule} from "@angular/platform-browser/animations";
import { WaitingComponent } from './waiting/waiting.component';
import { CurrentComponent } from './current/current.component';
import { DoneComponent } from './done/done.component';
import {SearchComponent} from "../lib/component/search/search.component";
import {MatAutocompleteModule} from "@angular/material/autocomplete";
import {MatFormFieldModule} from "@angular/material/form-field";
import {MatInputModule} from "@angular/material/input";
import { ThingComponent } from './thing/thing.component';
import {BasketComponent} from "../lib/component/basket/basket.component";
import {CardStripeComponent} from "../lib/component/card-stripe/card-stripe.component";


@NgModule({
    declarations: [
      AppComponent,
      HomeComponent,
      HeaderComponent,
      LoginComponent,
      SubscribeComponent,
      UnsubscribeComponent,
      ThingsComponent,
      CalendarComponent,
      CardComponent,
      SetupCompleteComponent,
      IncomeComponent,
      PaymentCardComponent,
      CoinComponent,
      CentPipe,
      AddComponent,
      PictureComponent,
      PicturesComponent,
      WaitingComponent,
      CurrentComponent,
      DoneComponent,
      SearchComponent,
      ThingComponent,
      BasketComponent,
      CardStripeComponent,

    ],
  imports: [
    BrowserModule,
    BrowserAnimationsModule,
    AppRoutingModule,
    FormsModule,
    ReactiveFormsModule,
    HttpClientModule,
    StoreModule.forRoot({login: loginReducer}, {}),
    NgbModule,
    NgbModalModule,
    CommonModule,
    ToastrModule.forRoot(),
    FileUploadModule,
    MatAutocompleteModule,
    MatFormFieldModule,
    MatInputModule,

  ],
    providers: [
      { provide: HTTP_INTERCEPTORS, useClass: AuthInterceptor, multi: true},
      {
        provide: 'routes',
        useValue: [
          'api/things',
          'api/url',
          'api/stars',
          'api/lasts',
          'api/proposed',
          'api/thing_types',
          'api/thing/all',
        ]
      },

    ],
  exports: [
    CalendarComponent,
    PicturesComponent
  ],
    bootstrap: [AppComponent]
})
export class AppModule { }
