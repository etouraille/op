import { Component, OnInit } from '@angular/core';
import {SubscribeComponent} from "../../lib/component/subscribe/subscribe.component";
import {HttpClient} from "@angular/common/http";
import {environment} from "../../environments/environment";

@Component({
  selector: 'app-bill',
  templateUrl: './bill.component.html',
  styleUrls: ['./bill.component.scss']
})
export class BillComponent extends SubscribeComponent implements OnInit {

  id: number = 0;
  bills: any[] = [];

  constructor(
    private http: HttpClient,
  ) {
    super();
  }

  ngOnInit(): void {
    this.get();
  }

  get(): void {
    this.add(this.http.get('api/bills').subscribe((data: any) => {
      this.bills = data['hydra:member'];
    }))
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
