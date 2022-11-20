import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import {AddComponent} from "./add/add.component";
import {LoggedComponent} from "./logged.component";
import {IsAdminGuard} from "../../guard/is-admin.guard";
import {ThingListComponent} from "./thing-list/thing-list.component";
import {ThingEditComponent} from "./thing-edit/thing-edit.component";
const routes: Routes = [


  {
    path: '',
    component: LoggedComponent,
    children: [
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
