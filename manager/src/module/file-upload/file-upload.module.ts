import {NgModule} from "@angular/core";
import {FileUploadComponent} from "./file-upload.component";
import {CommonModule} from "@angular/common";

@NgModule({
  declarations: [
    FileUploadComponent
  ],
  imports: [
    CommonModule,
  ],
  exports: [
    FileUploadComponent
  ]
})
export class FileUploadModule {}
