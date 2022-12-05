import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import {LoggedComponent} from "./logged.component";
import {EmptyComponent} from "./empty/empty.component";
import {UserComponent} from "./user/user.component";
import {UserCreateComponent} from "./user-create/user-create.component";
import {UserEditComponent} from "./user-edit/user-edit.component";

const routes: Routes = [{
  path: '',
  component: LoggedComponent,
  children: [{
    path: '',
    component: EmptyComponent,
  },{
    path: 'user-create',
    component : UserCreateComponent,
  },{
    path: 'user-edit/:id',
    component: UserEditComponent
  },{
    path: 'user',
    component: UserComponent
  }]
}];



@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class LoggedRoutingModule { }
