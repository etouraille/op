import { Component, OnInit } from '@angular/core';
import {SubscribeComponent} from "../../lib/component/subscribe/subscribe.component";
import {HttpClient} from "@angular/common/http";
import {ToastrService} from "ngx-toastr";
import {FormBuilder} from "@angular/forms";

@Component({
  selector: 'app-reset-password',
  templateUrl: './reset-password.component.html',
  styleUrls: ['./reset-password.component.scss']
})
export class ResetPasswordComponent extends SubscribeComponent implements OnInit {

  // TODO validator.
  form = this.fb.group({ email: ['']})

  constructor(
    private http: HttpClient,
    private toastR: ToastrService,
    private fb: FormBuilder,
  ) {
    super();
  }

  ngOnInit(): void {
  }

  submit() {
    this.add(this.http.get('reset/password/' + this.form.value.email).subscribe((data: any) => {
      if(data.success) {
        this.toastR.success('Un email vient de vous être envoyé');
      }
    }, (data: any) => this.toastR.error('Error' + data.error.error)))
  }
}
