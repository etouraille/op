import { NgModule } from '@angular/core';
import { BrowserModule } from '@angular/platform-browser';

import { AppRoutingModule } from './app-routing.module';
import { AppComponent } from './app.component';
import { HomeComponent } from './home/home.component';
import {HeaderComponent} from "../lib/component/header/header.component";
import { LoginComponent } from './login/login.component';
import { SubscribeComponent } from './subscribe/subscribe.component';
import { SubscribeComponent as UnsubscribeComponent } from './../lib/component/subscribe/subscribe.component';
import {ReactiveFormsModule} from "@angular/forms";
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

    ],
    imports: [
        BrowserModule,
        AppRoutingModule,
        ReactiveFormsModule,
        HttpClientModule,
        StoreModule.forRoot({login: loginReducer}, {}),
        NgbModule,
        NgbModalModule,
        CommonModule,
    ],
    providers: [
        [{provide: HTTP_INTERCEPTORS, useClass: AuthInterceptor, multi: true},]
    ],
    exports: [
        CalendarComponent
    ],
    bootstrap: [AppComponent]
})
export class AppModule { }
