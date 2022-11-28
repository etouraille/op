import { Component, OnInit } from '@angular/core';
import {SubscribeComponent} from "../../../lib/component/subscribe/subscribe.component";
import {HttpClient} from "@angular/common/http";
import {switchMap} from "rxjs";
import {DomSanitizer} from "@angular/platform-browser";
import {ToastrService} from "ngx-toastr";

@Component({
  selector: 'app-income',
  templateUrl: './income.component.html',
  styleUrls: ['./income.component.scss']
})
export class IncomeComponent extends SubscribeComponent implements OnInit {

  user: any = null;
  expenses: any[] = [];
  bill: any = null;
  url: any = null;

  constructor(
    private http: HttpClient,
    private sanitizer: DomSanitizer,
    private toastR: ToastrService,
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
      this.bill = this.expenses[0]?.incomeData?.file;
      if(this.bill) {
        setTimeout(() => {
          this.url =  this.sanitizer.bypassSecurityTrustResourceUrl( 'https://drive.google.com/viewerng/viewer?embedded=true&url=' +this.bill +'#toolbar=0&scrollbar=0') ;
        });
      }
    }))
  }

  payExpense() {
    this.add(
      this
        .http
        .get('api/expense/process?userId=' + this.user.id)
        .subscribe((data: any) => {
          if(data['hydra:member'][0].success) {
            this.toastR.success('Paiement réussi !');
          } else if(data['hydra:member'][0].error) {
            this.toastR.error(data['hydra:member'][0].error);
          }
          this.changeUserId(this.user.id);
        }));
  }

  generate() {
    this.add(this.http.get('api/export/income?userId=' + this.user.id).subscribe((data: any) => {
      this.changeUserId(this.user.id);
    }))
  }
}
