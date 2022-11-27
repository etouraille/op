import { Component, OnInit } from '@angular/core';
import {SubscribeComponent} from "../../../lib/component/subscribe/subscribe.component";
import {HttpClient} from "@angular/common/http";
import {FormBuilder} from "@angular/forms";

@Component({
  selector: 'app-user',
  templateUrl: './user.component.html',
  styleUrls: ['./user.component.scss']
})
export class UserComponent extends SubscribeComponent implements OnInit {

  form = this.fb.group({
    roles: [],
  })

  user: any = null;

  constructor(
    private http: HttpClient,
    private fb: FormBuilder,
  ) {
    super();
  }

  ngOnInit(): void {
  }

  selectUserId($event: number) {
    this.add(
      this
        .http
        .get('api/users/' + $event)
        .subscribe((user: any) => {
          this.form.patchValue(user);
          this.user = user;
        })
    )
  }

  patch(): void {
    this.add(
      this
        .http
        .patch('api/users/' + this.user.id , this.form.value)
        .subscribe((data: any) => {
          this.user = null;
          this.form.patchValue({});
        })
    )
  }
}
