import { Component, OnInit } from '@angular/core';
import {HttpClient} from "@angular/common/http";
import {SubscribeComponent} from "../../../component/subscribe/subscribe.component";
import {environment} from "../../../environments/environment";

@Component({
  selector: 'app-user',
  templateUrl: './user.component.html',
  styleUrls: ['./user.component.scss']
})
export class UserComponent extends SubscribeComponent implements OnInit {

  users: any[] = [];

  constructor(private http: HttpClient) {
    super();
  }

  ngOnInit(): void {
    this.add(
      this
        .http
        .get<any[]>(environment.api + '/api/users')
        .subscribe((data: any[]) => {
          // @ts-ignore
          this.users = data['hydra:member'];
      })
    )
  }

}
