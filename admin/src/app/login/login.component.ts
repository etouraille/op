import {Component, OnDestroy, OnInit} from '@angular/core';
import {FormControl, FormGroup} from "@angular/forms";
import {HttpClient} from "@angular/common/http";
import {environment} from "../../environments/environment";
import {StorageService} from "../../service/storage.service";
import {SubscribeComponent} from "../../component/subscribe/subscribe.component";
import {Router} from "@angular/router";

@Component({
  selector: 'app-login',
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.scss']
})
export class LoginComponent extends SubscribeComponent implements OnInit , OnDestroy  {

  private sub: any;

  loginForm = new FormGroup({
    username: new FormControl(''),
    password: new FormControl('')
  })

  constructor(private http: HttpClient, private store : StorageService, private router: Router) {
    super();
  }

  ngOnInit(): void {
  }

  onSubmit() {
    this.add(this
      .http.post(environment.api + '/api/login_check', this.loginForm.value)
      .subscribe((data: any) => {
        if(data.token) {
          this
            .store
            .set('token', data.token)
          this.router.navigate(['/admin']);
        } else {}
      }, (error) => { this.loginForm.patchValue({ username: '', password : ''});})
    )
  }
}
