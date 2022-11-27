import { Component, OnInit } from '@angular/core';
import {SubscribeComponent} from "../../lib/component/subscribe/subscribe.component";
import {HttpClient} from "@angular/common/http";

@Component({
  selector: 'app-coin',
  templateUrl: './coin.component.html',
  styleUrls: ['./coin.component.scss']
})
export class CoinComponent extends SubscribeComponent implements OnInit {

  coins: any[] = [];
  solde: number = 0;
  constructor(
    private http: HttpClient
  ) {
    super();
  }

  ngOnInit(): void {
    this.add(
      this.http.get('api/coins').subscribe((data: any) => {
        this.coins = data['hydra:member'];
        this.solde = this.coins.reduce((a, b) => a + b.amount, 0 )
      })
    )
  }

}
