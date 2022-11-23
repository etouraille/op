import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { AddComponent } from './add/add.component';
import { LoggedRoutingModule} from './logged-routing.module'
import {LoggedComponent} from "./logged.component";
import {FormsModule, ReactiveFormsModule} from "@angular/forms";
import {FileUploadModule} from "../../lib/module/file-upload/file-upload.module";
import { ThingListComponent } from './thing-list/thing-list.component';
import {PictureComponent} from "../../lib/component/picture/picture.component";
import { ThingEditComponent } from './thing-edit/thing-edit.component';
import {PicturesComponent} from "../../lib/component/pictures/pictures.component";
import { ThingSearchComponent } from './thing-search/thing-search.component';
import {SearchComponent} from "../../lib/component/search/search.component";
import {MatFormFieldModule} from "@angular/material/form-field";
import {MatAutocompleteModule} from "@angular/material/autocomplete";
import {MatInputModule} from "@angular/material/input";
import {BrowserAnimationsModule, NoopAnimationsModule} from "@angular/platform-browser/animations";
import {BrowserModule} from "@angular/platform-browser";
import { ThingComponent } from './thing/thing.component';
import {EditorComponent} from "../../lib/component/editor/editor.component";
import {CalendarComponent} from "../../lib/component/calendar/calendar.component";
import {NgbModalModule} from "@ng-bootstrap/ng-bootstrap";
import {WhoComponent} from "../../lib/component/who/who.component";
import { ThingOutComponent } from './thing-out/thing-out.component';
import {WhoModalComponent} from "../../lib/component/who-modal/who-modal.component";
import { ThingBackComponent } from './thing-back/thing-back.component';
import { IncomeComponent } from './income/income.component';


@NgModule({
  declarations: [
    AddComponent,
    LoggedComponent,
    ThingListComponent,
    PictureComponent,
    PicturesComponent,
    ThingEditComponent,
    ThingSearchComponent,
    SearchComponent,
    ThingComponent,
    EditorComponent,
    CalendarComponent,
    WhoComponent,
    WhoModalComponent,
    ThingOutComponent,
    ThingBackComponent,
    IncomeComponent,
  ],
  imports: [
    CommonModule,
    LoggedRoutingModule,
    FormsModule,
    ReactiveFormsModule,
    FileUploadModule,
    MatFormFieldModule,
    MatAutocompleteModule,
    MatInputModule,
    NgbModalModule,
  ], exports: [
    FormsModule,
  ]
})
export class LoggedModule {}
