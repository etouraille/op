import { Component, OnInit } from '@angular/core';
import {FormBuilder, Validators} from "@angular/forms";
import {Router} from "@angular/router";
import {StorageService} from "../../utils/service/storage.service";
import {SubscribeComponent} from "../../utils/component/subscribe/subscribe.component";
import {tap} from "rxjs";
import {HttpClient} from "@angular/common/http";
import {Store} from "@ngrx/store";
import {login} from "../../utils/actions/login-action";

@Component({
  selector: 'app-login',
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.scss']
})
export class LoginComponent extends SubscribeComponent implements OnInit {


  loginForm = this.fb.group({
    username: ['', Validators.required],
    password: ['', Validators.required],
  })

  constructor(
    private fb: FormBuilder,
    private router : Router,
    private storage: StorageService,
    private http: HttpClient,
    private store:  Store<{logged:boolean}>
  ) {
    super();
  }

  ngOnInit(): void {
  }

  submit() {
    this.add(
      this.http
        .post('api/login_check', this.loginForm.value)
        .pipe(tap((data: any)=> {
          if(data.token) {
            this.storage.set('token', data.token);
            this.router.navigate(['/']).then(() => {
              this.store.dispatch(login());
            });
          }
        }))
        .subscribe()
    )
  }
}
