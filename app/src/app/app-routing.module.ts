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
import {MemberGuard} from "../lib/guard/member.guard";
import {AddComponent} from "./add/add.component";
import {WaitingComponent} from "./waiting/waiting.component";
import {CurrentComponent} from "./current/current.component";
import {DoneComponent} from "./done/done.component";

const routes: Routes = [{
  path: '',
  component: HomeComponent,
  children: [
    {
      path: 'done',
      component: DoneComponent,
      canActivate: [LoggedGuard]
    },
    {
      path: 'current',
      component: CurrentComponent,
      canActivate: [LoggedGuard]
    },
    {
      path: 'waiting',
      component: WaitingComponent,
      canActivate: [LoggedGuard]
    },
    {
      path: 'add',
      component: AddComponent,
      canActivate: [LoggedGuard, MemberGuard]
    },
    {
      path: 'coin',
      component: CoinComponent,
      canActivate: [LoggedGuard, MemberGuard]
    },
    { path: 'income', component: IncomeComponent, canActivate: [LoggedGuard, MemberGuard]},
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
