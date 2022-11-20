import {ChangeDetectorRef, Component, ElementRef, forwardRef, OnInit, ViewChild} from '@angular/core';
import { FileUploadService } from './file-upload.service';
import {SubscribeComponent} from "../../component/subscribe/subscribe.component";
import {DomSanitizer} from "@angular/platform-browser";
import {tap} from "rxjs";
import {ControlValueAccessor, NG_VALUE_ACCESSOR} from "@angular/forms";
import {environment} from "../../environments/environment";

@Component({
  selector: 'app-file-upload',
  templateUrl: './file-upload.component.html',
  providers: [{
    provide: NG_VALUE_ACCESSOR,
    useExisting: forwardRef(() => FileUploadComponent),
    multi: true
  }]
})
export class FileUploadComponent extends SubscribeComponent implements OnInit, ControlValueAccessor {

  // Variable to store shortLink from api response
  shortLink: string = "";
  loading: boolean = false; // Flag variable
  file: string|Blob = ''; // Variable to store file
  image: any;
  dtk: any;
  _file: any;

  // @ts-ignore
  @ViewChild("file") uploadInput: ElementRef;
  // Inject service
  constructor(
    private fileUploadService: FileUploadService,
  ) {

    super();
  }

  onChangeCVA: any = () => {};
  onTouchCVA: any = () => {};

  ngOnInit(): void {
    this.dtk = Date.now();
  }

  writeValue(obj: any) {
    if(obj) {
      setTimeout(() => {
        this._file = obj.picture;
        this.image = environment.cdn + obj.picture;
        this.shortLink = obj.picture;
      }, 100);
    }
  }

  registerOnChange(fn: any) {
    this.onChangeCVA = fn;
  }

  registerOnTouched(fn: any) {
    this.onTouchCVA = fn;
  }

  // On file Select
  onChange(event: any) {
    this.file = event.target.files[0];
    this.loading = !this.loading;
    this.add(
      this.fileUploadService.upload(this.file).pipe(tap((event: any) => {
        if (typeof (event) === 'object') {
          // Short link via api response
          this.shortLink = event.data.name;
          let pic = { picture: event.data.name };
          console.log(pic);
          this.onChangeCVA(pic);
          setTimeout(() => {
            this.image = environment.cdn + event.data.name + '?dtk=' + Date.now();
          }, 100)
          this.loading = false; // Flag variable
        }
      })).subscribe()
    );
  }

  // OnClick of button Upload
  onUpload() {
    this.uploadInput.nativeElement.click();

  }
}
