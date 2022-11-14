import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import {AddComponent} from "./add/add.component";
import {LoggedComponent} from "./logged.component";
import {IsAdminGuard} from "../../guard/is-admin.guard";
const routes: Routes = [


  {
    path: '',
    component: LoggedComponent,
    children: [
      {
        path: 'add',
        component: AddComponent,

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
