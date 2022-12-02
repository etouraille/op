import { Component, OnInit } from '@angular/core';
import {SubscribeComponent} from "../../../lib/component/subscribe/subscribe.component";
import {HttpClient} from "@angular/common/http";
import {FormBuilder, FormControl} from "@angular/forms";
import {ToastrService} from "ngx-toastr";

@Component({
  selector: 'app-user',
  templateUrl: './user.component.html',
  styleUrls: ['./user.component.scss']
})
export class UserComponent extends SubscribeComponent implements OnInit {

  form = this.fb.group({
    roles: new FormControl([]),
    isMemberValidated: [null]
  })

  user: any = null;
  isMember: boolean = false;
  _roles : never[] = [];


  constructor(
    private http: HttpClient,
    private fb: FormBuilder,
    private toastR: ToastrService,
  ) {
    super();
  }

  ngOnInit(): void {
  }

  getUser(id: any) {
    this.add(
      this
        .http
        .get('api/users/' + id)
        .subscribe((user: any) => {
          this.form.patchValue(user);
          this.user = user;
          this.isMember = this.user.roles.includes('ROLE_MEMBER');
          this._roles = this.user.roles;
        })
    )
  }

  selectUserId($event: number) {
    this.getUser($event);
    this.add(
      this.form.valueChanges.subscribe((data: any) => {
        //this.changeRoles(data.roles);

      })
    )
  }

  patch(): void {
    this.add(
      this
        .http
        .patch('api/users/' + this.user.id , this.form.value)
        .subscribe((data: any) => {
            this.form.patchValue(data);
            this.toastR.success('Droits modifiés');
        })
    )
  }

  changeRoles($event: any) {
    this._roles = $event;
    this.isMember = $event.includes('ROLE_MEMBER');
    if(!this.isMember) {
      this.form.patchValue({roles: this._roles, isMemberValidated: null});
    }
  }
}
