import { Component, OnInit } from '@angular/core';
import {SubscribeComponent} from "../../../lib/component/subscribe/subscribe.component";
import {HttpClient} from "@angular/common/http";
import {switchMap} from "rxjs";

@Component({
  selector: 'app-income',
  templateUrl: './income.component.html',
  styleUrls: ['./income.component.scss']
})
export class IncomeComponent extends SubscribeComponent implements OnInit {

  user: any = null;
  expenses: any[] = [];

  constructor(
    private http: HttpClient,
  ) {
    super();
  }

  ngOnInit(): void {
  }

  changeUserId($event: number) {
    this.add(this.http.get('api/users/' + $event).pipe(switchMap((user: any) => {
      this.user = user;
      return this.http.get('api/expenses?userId=' + user.id);
    })).subscribe((data: any) => {
      this.expenses = data['hydra:member'];
    }))
  }

  payExpense() {
    this.add(this.http.get('api/expenses/process?userId=' + this.user.id).subscribe());
  }
}
