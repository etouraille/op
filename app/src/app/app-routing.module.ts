import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import {HomeComponent} from "./home/home.component";
import {LoginComponent} from "./login/login.component";
import {SubscribeComponent} from "./subscribe/subscribe.component";
import {ThingsComponent} from "./things/things.component";
import {CardComponent} from "./card/card.component";
import {SetupCompleteComponent} from "./setup-complete/setup-complete.component";
import {IncomeComponent} from "./income/income.component";
import {CoinComponent} from "./coin/coin.component";
import {LoggedGuard} from "../lib/guard/logged.guard";

const routes: Routes = [{
  path: '',
  component: HomeComponent,
  children: [
    {
      path: 'coin',
      component: CoinComponent,
      canActivate: [LoggedGuard]
    },
    { path: 'income', component: IncomeComponent, canActivate: [LoggedGuard]},
    { path: 'setup-complete', component: SetupCompleteComponent, canActivate: [LoggedGuard]},
    { path: 'card', component: CardComponent, canActivate: [LoggedGuard]},
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
