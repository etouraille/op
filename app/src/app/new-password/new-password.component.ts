import { Component, OnInit } from '@angular/core';
import {SubscribeComponent} from "../../lib/component/subscribe/subscribe.component";
import {HttpClient} from "@angular/common/http";
import {ToastrService} from "ngx-toastr";
import {ActivatedRoute, Router} from "@angular/router";
import {FormBuilder} from "@angular/forms";

@Component({
  selector: 'app-new-password',
  templateUrl: './new-password.component.html',
  styleUrls: ['./new-password.component.scss']
})
export class NewPasswordComponent extends SubscribeComponent implements OnInit {

  token: any = '';
  form = this.fb.group({password: ['']});

  constructor(
    private http: HttpClient,
    private toastR: ToastrService,
    private router: Router,
    private route: ActivatedRoute,
    private fb: FormBuilder
  ) {
    super();
  }

  ngOnInit(): void {
    this.add(this.route.queryParams.subscribe((param: any) => {
      this.token = param.token;
    }))
  }

  submit() {
    this.add(this.http.post('reset/password', { token: this.token, password: this.form.value.password }).subscribe((data: any) => {
      if(data.success) {
        this.toastR.success('Votre mot de passe viens d\'être changé !');
        this.router.navigate(['login']);
      } else {
        this.toastR.error('error ' + data.error);
      }
    }))
  }
}
