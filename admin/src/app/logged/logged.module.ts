import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { LoggedRoutingModule } from "./logged-routing.module";
import { EmptyComponent } from './empty/empty.component';
import { UserComponent } from './user/user.component';
import { UserCreateComponent } from './user-create/user-create.component';
import {FormsModule, ReactiveFormsModule} from "@angular/forms";
import { UserEditComponent } from './user-edit/user-edit.component';
import {AppRolesComponent} from "../../component/app-roles/app-roles.component";
import {NgbToastModule} from "@ng-bootstrap/ng-bootstrap";



@NgModule({
  declarations: [
    EmptyComponent,
    UserComponent,
    UserCreateComponent,
    UserEditComponent,
    AppRolesComponent,

  ],
  imports: [
    CommonModule,
    LoggedRoutingModule,
    FormsModule,
    ReactiveFormsModule,
    NgbToastModule

  ]
})
export class LoggedModule { }
