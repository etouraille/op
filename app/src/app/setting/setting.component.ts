import { Component, OnInit } from '@angular/core';
import {SubscribeComponent} from "../../lib/component/subscribe/subscribe.component";
import {FormBuilder} from "@angular/forms";
import {HttpClient} from "@angular/common/http";
import {Store} from "@ngrx/store";
import {switchMap} from "rxjs";
import * as _ from 'lodash';
import { user as setUser } from "../../lib/actions/user-action";
import {Router} from "@angular/router";

@Component({
  selector: 'app-setting',
  templateUrl: './setting.component.html',
  styleUrls: ['./setting.component.scss']
})
export class SettingComponent extends SubscribeComponent implements OnInit {
  formGroup = this.fb.group({ role: ['ROLE_USER']});

  user: any;

  constructor(
    private fb: FormBuilder,
    private http: HttpClient,
    private store: Store<{ login: any}>,
    private router: Router,

  ) {
    super();
  }

  ngOnInit(): void {

    this.add(
      this.store.select((data: any) => data.login.user).pipe(switchMap((user: any) => {
        return this.http.get('api/users/' + user.id );
      })).subscribe((user: any) => {
        this.user = user;
        this.formGroup.patchValue({role: user.roles.includes('ROLE_MEMBER') ? 'ROLE_MEMBER': 'ROLE_USER'});
      })
    );

  }

  submit() {
      let role = this.formGroup.value.role;
      let roles = this.user.roles;
      if(role === "ROLE_USER") roles.splice(roles.indexOf('ROLE_MEMBER'), 1);
      roles.push(role)
      roles = _.uniq(roles);
      this.add(this.http.patch('api/users/' + this.user.id, { roles }).subscribe((user: any) => {
      this.user = user;
      this.store.dispatch(setUser({ user}));
      if(this.user.roles.includes('ROLE_MEMBER')) {
        this.router.navigate(['/add']);
      }
    }));
  }

}
