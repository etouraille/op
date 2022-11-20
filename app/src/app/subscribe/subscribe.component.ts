import { Component, OnInit } from '@angular/core';
import {FormBuilder, Validators} from "@angular/forms";
import { SubscribeComponent as UnsubscribeComponent } from "../../utils/component/subscribe/subscribe.component";
import {HttpClient} from "@angular/common/http";
import {Router} from "@angular/router";

@Component({
  selector: 'app-subscribe',
  templateUrl: './subscribe.component.html',
  styleUrls: ['./subscribe.component.scss']
})
export class SubscribeComponent extends UnsubscribeComponent implements OnInit {

  subscribeForm: any = this.fb.group({
    email: ['', Validators.required],
    password : ['', Validators.required],
    confirmPassword: ['', Validators.required],
    role: ['', Validators.required],
  });

  constructor(
    private fb: FormBuilder,
    private http: HttpClient,
    private router: Router,
  ) {
    super();
  }

  ngOnInit(): void {
  }

  submit(): void {

    let object = Object.assign({}, this.subscribeForm.value);
    object.roles = [object.role];
    delete object.role;
    // TODO faille de securité ... on peut créer un role admin avec l'api.

    this.add(
      this
        .http
        .post('api/users', object)
        .subscribe((data:any) => {
          this.router.navigate(['/login']);
        })
    );
  }

}
