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
  incomes: any[] = [];
  solde: number = 0;
  isAccountActive: boolean = false;

  constructor(
    private http: HttpClient
  ) {
    super();
  }

  ngOnInit(): void {
    this.add(this.http.get('api/customer/account/url').subscribe((data: any) => {
      this.url = data.url;
    }))
    this.add(
      this
        .http
        .get('api/incomes')
        .subscribe((data: any) => {
          this.incomes = data['hydra:member'];
          this.solde = this.incomes.reduce((a, b) => a + b.amount , 0);
        })
    )
    this.add(
      this.http.get('api/account/is/active').subscribe((data: any) => {
        this.isAccountActive = data.isActive;
      })
    )
  }

}
