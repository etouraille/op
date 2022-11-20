import {Component, OnDestroy, OnInit} from '@angular/core';

@Component({
  selector: 'app-subscribe',
  templateUrl: './subscribe.component.html',
  styleUrls: ['./subscribe.component.scss']
})
export class SubscribeComponent implements OnDestroy{

  protected subs : any[] = [];

  constructor() {}

  add(subscription: any ) {
    this.subs.push(subscription);
  }

  ngOnDestroy(): void {
    this.subs.forEach((sub: any) => {
      sub.unsubscribe();
    })
  }
}
