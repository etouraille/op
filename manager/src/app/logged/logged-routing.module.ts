import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import {AddComponent} from "./add/add.component";
import {LoggedComponent} from "./logged.component";
import {IsAdminGuard} from "../../guard/is-admin.guard";
import {ThingListComponent} from "./thing-list/thing-list.component";
import {ThingEditComponent} from "./thing-edit/thing-edit.component";
import {ThingSearchComponent} from "./thing-search/thing-search.component";
import {ThingComponent} from "./thing/thing.component";
import {ThingOutComponent} from "./thing-out/thing-out.component";
import {ThingBackComponent} from "./thing-back/thing-back.component";
import {IncomeComponent} from "./income/income.component";
import {UserComponent} from "./user/user.component";
import {PendingComponent} from "./pending/pending.component";
import {TypeComponent} from "./type/type.component";
import {UserAddComponent} from "./user-add/user-add.component";
import {CardStripeComponent} from "../../lib/component/card-stripe/card-stripe.component";
import {CompensationComponent} from "./compensation/compensation.component";
const routes: Routes = [


  {
    path: '',
    component: LoggedComponent,
    children: [
      {
        path: 'compensation',
        component: CompensationComponent,
      },
      {
        path: 'card/:secret',
        component: CardStripeComponent,
      },
      {
        path: 'type/:id',
        component: TypeComponent,
      },
      {
        path: 'type-add',
        component: TypeComponent,
      },
      {
        path: 'user-add',
        component: UserAddComponent,
      },
      {
        path: 'pending',
        component: PendingComponent,
      },
      {
        path: 'user',
        component: UserComponent,
      },{
        path: 'income',
        component: IncomeComponent,
      },
      {
        path: 'back',
        component: ThingBackComponent,
      },
      {
        path: 'out',
        component: ThingOutComponent,
      },
      {
        path: 'search',
        component: ThingSearchComponent,
      },
      {
        path: 'thing/:id',
        component: ThingComponent,
      },
      {
        path: 'add',
        component: AddComponent,

      },{
        path: 'thing-list',
        component : ThingListComponent,
      },{
        path: 'thing-edit/:id',
        component : ThingEditComponent,
      }
    ],
    canActivate: [
      IsAdminGuard,
    ]
  },

];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class LoggedRoutingModule { }
