import { Component, OnInit } from '@angular/core';
import {ActivatedRoute, Router} from "@angular/router";
import {Observable, switchMap} from "rxjs";
import {HttpClient} from "@angular/common/http";
import {environment} from "../../../environments/environment";
import {SubscribeComponent} from "../../../component/subscribe/subscribe.component";
import {FormBuilder} from "@angular/forms";

@Component({
  selector: 'app-user-edit',
  templateUrl: './user-edit.component.html',
  styleUrls: ['./user-edit.component.scss']
})
export class UserEditComponent extends SubscribeComponent implements OnInit {

  $user: Observable<any>;
  user: any;

  userForm = this.fb.group({
    id: [''],
    username: [''],
    password: [''],
    roles: [[]]
  })

  constructor(
    private route: ActivatedRoute,
    private http: HttpClient,
    private fb: FormBuilder,
    private router: Router
  ) {
    super();
    this.$user = this.route.paramMap.pipe(switchMap((param: any) => {
      return  this.http.get(environment.api + '/api/users/' + param.get('id'));
    }))

  }

  ngOnInit(): void {
    this.add(
      this.$user.subscribe(data => {
        this.user = data;
        this.userForm.patchValue({id: data.id , username: data.email, password: '', roles: data.roles });
      })
    );
  }

  submit() {
    this.add(
      this
        .http
        .put(environment.api + '/api/users/' + this.user.id, this.userForm.value)
        .subscribe( data => {
          this.router.navigate(['/admin/user'])
        })
      )
  }

}
