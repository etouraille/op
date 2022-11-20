import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { AddComponent } from './add/add.component';
import { LoggedRoutingModule} from './logged-routing.module'
import {LoggedComponent} from "./logged.component";
import {FormsModule, ReactiveFormsModule} from "@angular/forms";
import {FileUploadModule} from "../../module/file-upload/file-upload.module";
import { ThingListComponent } from './thing-list/thing-list.component';
import {PictureComponent} from "../../component/picture/picture.component";
import { ThingEditComponent } from './thing-edit/thing-edit.component';
import {PicturesComponent} from "../../component/pictures/pictures.component";


@NgModule({
  declarations: [
    AddComponent,
    LoggedComponent,
    ThingListComponent,
    PictureComponent,
    PicturesComponent,
    ThingEditComponent,
  ],
  imports: [
    CommonModule,
    LoggedRoutingModule,
    FormsModule,
    ReactiveFormsModule,
    FileUploadModule,
  ], exports: [
    FormsModule,
  ]
})
export class LoggedModule {}
