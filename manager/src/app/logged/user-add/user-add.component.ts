import { Component, OnInit } from '@angular/core';
import {SubscribeComponent} from "../../../lib/component/subscribe/subscribe.component";
import {HttpClient} from "@angular/common/http";
import {ToastrService} from "ngx-toastr";
import {FormBuilder} from "@angular/forms";

@Component({
  selector: 'app-user-add',
  templateUrl: './user-add.component.html',
  styleUrls: ['./user-add.component.scss']
})
export class UserAddComponent extends SubscribeComponent implements OnInit {
  userForm: any = this.fb.group({
    firstname: [],
    lastname: [],
    email: [],
    roles: [],
    address: [],
    city: [],
    zipcode: [],

  });

  constructor(
    private http: HttpClient,
    private toastR: ToastrService,
    private fb: FormBuilder
  ) {
    super();
  }

  ngOnInit(): void {
  }

  submit() {

    let object = this.userForm.value;
    this.add(this.http.post('api/users',object).subscribe(
      () => {
        this.toastR.success('Utilisateur ajouté');
        this.userForm.patchValue({});
      },(error: any) => {
        this.toastR.error(error);
      }
    ))

  }

}
