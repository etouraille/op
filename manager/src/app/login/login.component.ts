import { Component, OnInit } from '@angular/core';
import {FormControl, FormGroup} from "@angular/forms";
import {HttpClient} from "@angular/common/http";
import {StorageService} from "../../service/storage.service";
import {Router} from "@angular/router";
import {SubscribeComponent} from "../../lib/component/subscribe/subscribe.component";

@Component({
  selector: 'app-login',
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.scss']
})
export class LoginComponent extends SubscribeComponent implements OnInit {

  loginForm = new FormGroup({
    username:new FormControl(''),
    password: new FormControl('')
  })
  constructor(
    private http:HttpClient,
    private service: StorageService,
    private router : Router ) {
    super();
  }

  ngOnInit(): void {
  }

  onSubmit(): void {
    this.add(this
      .http
      .post('api/login_check', this.loginForm.value)
      .subscribe((data: any) => {
        if(data.token) {
          this.service.set('token', data.token);
          this.router.navigate(['/logged/add']);
        }
      },error => {
        //this.loginForm.patchValue({username: '', password: ''});
      })
    );
  }
}
