import { Component, OnInit } from '@angular/core';
import {SubscribeComponent} from "../../lib/component/subscribe/subscribe.component";
import {HttpClient} from "@angular/common/http";

@Component({
  selector: 'app-income',
  templateUrl: './income.component.html',
  styleUrls: ['./income.component.scss']
})
export class IncomeComponent extends SubscribeComponent implements OnInit {

  url: any = null;

  constructor(
    private http: HttpClient
  ) {
    super();
  }

  ngOnInit(): void {
    this.add(this.http.get('api/customer/account/url').subscribe((data: any) => {
      this.url = data.url;
    }))
  }

}
