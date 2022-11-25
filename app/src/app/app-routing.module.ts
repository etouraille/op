import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import {HomeComponent} from "./home/home.component";
import {LoginComponent} from "./login/login.component";
import {SubscribeComponent} from "./subscribe/subscribe.component";
import {ThingsComponent} from "./things/things.component";
import {CardComponent} from "./card/card.component";
import {SetupCompleteComponent} from "./setup-complete/setup-complete.component";
import {IncomeComponent} from "./income/income.component";

const routes: Routes = [{
  path: '',
  component: HomeComponent,
  children: [
    { path: 'income', component: IncomeComponent},
    { path: 'setup-complete', component: SetupCompleteComponent},
    { path: 'card', component: CardComponent},
    { path: 'login', component: LoginComponent},
    { path: 'subscribe', component: SubscribeComponent},
    { path: '', component: ThingsComponent}
  ]
}];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule]
})
export class AppRoutingModule { }
