import { Component, OnInit } from '@angular/core';
import {SubscribeComponent} from "../../../lib/component/subscribe/subscribe.component";
import {HttpClient} from "@angular/common/http";
import {switchMap} from "rxjs";
import {DomSanitizer} from "@angular/platform-browser";
import {ToastrService} from "ngx-toastr";
import {Router} from "@angular/router";

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
  total: number = 0;

  constructor(
    private http: HttpClient,
    private sanitizer: DomSanitizer,
    private toastR: ToastrService,
    private router: Router,
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
      this.total = this.expenses.reduce((a: any, b: any) => a + b.amount, 0);
      this.bill = this.expenses[0]?.incomeData?.file;
      if(this.bill) {
        setTimeout(() => {
          this.url =  this.getUrl(this.bill)
        });
      }
    }))
  }

  getUrl(bill: any) {
    return this.sanitizer.bypassSecurityTrustResourceUrl( 'https://drive.google.com/viewerng/viewer?embedded=true&url=' + bill +'#toolbar=0&scrollbar=0') ;
  }

  payExpense() {
    this.add(
      this
        .http
        .get('api/expense/process?userId=' + this.user.id)
        .subscribe((data: any) => {
          if(data['hydra:member'][0].success) {
            this.toastR.success('Paiement réussi !');
            setTimeout(() => {
              this.url = this.getUrl(data['hydra:member'][0].bill);
            })
          } else if(data['hydra:member'][0].error) {
            this.toastR.error(data['hydra:member'][0].error);
            this.router.navigate(['card/' + data['hydra:member'][0].error.id ], { queryParams : { redirect: 'logged/thing-list'}});
          }
          this.changeUserId(this.user.id);
        }));
  }

  generate() {
    this.add(this.http.get('api/export/income?userId=' + this.user.id).subscribe((data: any) => {
      this.changeUserId(this.user.id);
    }))
  }

  markAsPaid() {
    this.add(this.http.get('api/mark-as-paid?userId=' + this.user.id).subscribe(() => {
      this.changeUserId(this.user.id);
    }))
  }
}
