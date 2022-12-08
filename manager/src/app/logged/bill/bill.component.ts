import { Component, OnInit } from '@angular/core';
import {SubscribeComponent} from "../../../lib/component/subscribe/subscribe.component";
import {HttpClient} from "@angular/common/http";
import {environment} from "../../../environments/environment";

@Component({
  selector: 'app-bill',
  templateUrl: './bill.component.html',
  styleUrls: ['./bill.component.scss']
})
export class BillComponent extends SubscribeComponent implements OnInit {

  bills : any[] = [];
  id: number = 0;

  constructor(
    private http: HttpClient
  ) {
    super();
  }

  ngOnInit() {
  }

  get(userId: any): void {
    this.add(this.http.get('api/bills?userId=' + userId).subscribe((data: any) => {
      this.bills = data['hydra:member'];
    }))
  }

  selectUser($event: number) {
    this.get($event);
  }

  next() {
    if(this.id < this.bills.length - 1 ) {
      this.id ++;
    }
  }

  previous() {
    if(this.id > 0) {
      this.id --;
    }
  }

  file() {
    return environment.cdn + this.bills[this.id].file;
  }
}
