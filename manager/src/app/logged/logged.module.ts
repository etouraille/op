import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { AddComponent } from './add/add.component';
import { LoggedRoutingModule} from './logged-routing.module'
import {LoggedComponent} from "./logged.component";
import {ReactiveFormsModule} from "@angular/forms";
import {FileUploadModule} from "../../module/file-upload/file-upload.module";


@NgModule({
  declarations: [
    AddComponent,
    LoggedComponent
  ],
  imports: [
    CommonModule,
    LoggedRoutingModule,
    ReactiveFormsModule,
    FileUploadModule,
  ]
})
export class LoggedModule {}
