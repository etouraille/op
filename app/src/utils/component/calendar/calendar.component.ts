import {Component, Input, OnChanges, OnInit, SimpleChanges} from '@angular/core';
import * as moment from 'moment';
import {NgbActiveModal} from "@ng-bootstrap/ng-bootstrap";
@Component({
  selector: 'app-calendar',
  templateUrl: './calendar.component.html',
  styleUrls: ['./calendar.component.scss']
})
export class CalendarComponent implements OnInit {

  month: any[] = [];
  currentDay: any;
  currentDate: Date = new Date();
  click: number = 0;
  @Input() startDate: Date|null = null;
  @Input() endDate: Date|null = null;
  @Input() reservations: any[] = [];

  constructor(public activeModal: NgbActiveModal) { }

  ngOnInit(): void {

    this.initMonth(this.currentDate);
    this.setReservations();
  }


  book(): void {
    this.activeModal.close({ startDate: this.startDate, endDate: this.endDate});
  }


  setReservations() {
    let _reservations = this.reservations.map(element => (
        {
          ...element,
          startDate : new Date(element.startDate),
          endDate: new Date(element.endDate)
        }
    ))
    _reservations.forEach((reservation: any) => {
      this.month.forEach((week: any, i: number) => {
        week.forEach((date: any, j: number) => {
          let momentStartDate = moment(reservation.startDate);
          let momentEndDate = moment(reservation.endDate);
          let momentDate = moment(date.date);
          if(momentStartDate.isSameOrBefore(momentDate, 'day') && momentEndDate.isSameOrAfter(momentDate, 'day')) {
            this.month[i][j].blocked = true;
          }
          if(momentStartDate.isSame(momentDate, 'day')) {
            this.month[i][j].start = true;
          }
          if(momentEndDate.isSame(momentDate, 'day')) {
            this.month[i][j].end = true;
          }
        })
      })
    })
    console.log( this.month);
  }

  initMonth(date: Date ) {
    this.currentDay = this._date(date, 0);
    let _dayZero = this._date(new Date(), 0);
    let _currentDay = this._date(date, 0).d;

    let firstDay = this._date(date, 0,true);
    let lastDay = this._date(date, 0, false, true );
    let indexFirstDay = _currentDay + firstDay.number - 1;
    let indexLastDay = lastDay.d - _currentDay  + 6 + 7 - lastDay.number;

    let days = [];

    for(let i = -1 * indexFirstDay; i <= indexLastDay; i++) {
      let _date = this._date(date,i);
      days.push(_date);

    }

    let month: any[] = [];
    let week: any[] = [];
    let currentWeek = days[0].w;
    days.forEach((_date: any , index) => {

      _date = Object.assign({today: this.equal(_date, _dayZero),
        outside: _dayZero.month !== _date.month }, _date, { d: _date.d < 10 ? '0' + _date.d: _date.d});

      if(currentWeek === _date.w) {
        week.push(_date);
      } else if(currentWeek !== _date.w || index === (days.length - 1)) {
        month.push(week);
        week = [];
        week.push(_date);
        currentWeek = _date.w;
      }
    })

    this.month = month;
    this.setSelected();

  }

  equal(a: any, b: any ) {
    return a.year === b.year
    && a.month == b.month
    && a.d == b.d
    && a.number == b.number;
  }

  _date(date: Date, add: any, firstDay: boolean = false, lastDay: boolean = false ) {


    if(lastDay) {
      return {
        year : moment(date).endOf('month').year(),
        month : moment(date).endOf('month').month(),
        day: moment(date).endOf('month').format('dd'),
        number: moment(date).endOf('month').day(),
        d: moment(date).endOf('month').date(),
        w: moment(date).endOf('month').week()

      }
    }
    if(firstDay) {
      return {
        year : moment(date).startOf('month').year(),
        month : moment(date).startOf('month').month(),
        day: moment(date).startOf('month').format('dd'),
        number: moment(date).startOf('month').day(),
        d: moment(date).startOf('month').date(),
        w: moment(date).endOf('month').week()
      }
    }

    return {
      year : moment(date).add(add, 'day').year(),
      month : moment(date).add(add, 'day').month(),
      day: moment(date).add(add, 'day').format('dd'),
      number: moment(date).add(add, 'day').day(),
      d: moment(date).add(add, 'day').date(),
      w: moment(date).add(add, 'day').week(),
      date: moment(date).add(add, 'day').toDate(),
    }
  }

  addYear() {
    this.initMonth(this.currentDate = moment(this.currentDate).add(1, 'year').toDate());
    this.setReservations();
  }

  removeYear() {
    this.initMonth(this.currentDate = moment(this.currentDate).add(-1, 'year').toDate());
    this.setReservations();
  }

  setMonth(number: number ): void {
    let currentMonth = this._date(this.currentDate, 0).month;
    let deltaMonth = number - currentMonth;
    this.initMonth(this.currentDate = moment(this.currentDate).add(deltaMonth, 'month').toDate());
    this.setReservations();
  }

  selectDay($event: MouseEvent, date: any, i:number, j:number) {
    if(this.month[i][j].blocked) {
      return;
    }
    switch(this.click) {
      case 0 :
      case 1 :
        this.click ++;
        break;
      default:
        this.click = 0;
        break;
    }
    if(this.click == 1) {
      this.startDate = date.date;
      this.setSelected();
    }
    if(this.click == 2) {
      this.endDate = date.date;
      this.setSelected();
    }
    if(this.click == 0) {
      this.startDate = null;
      this.endDate = null;
      this.setSelected();
    }
  }

  setSelected() {
    if(this.startDate && this.endDate) {
      this.month.forEach((week: any, i: number) => {
        week.forEach((day: any, j:number) => {
          let startDate = moment(this.startDate);
          let endDate = moment(this.endDate);
          let date = moment(day.date);
          if(date.isSameOrAfter(startDate) && date.isSameOrBefore(endDate)) {
            this.month[i][j].selected = true;
          }
          if(date.isSame(startDate)) {
            this.month[i][j].startSelected = true;
          }
          if(date.isSame(endDate)) {
            this.month[i][j].endSelected = true;
          }
        })
      })
    } else if(this.startDate) {
      this.month.forEach((week: any, i: number) => {
        week.forEach((day: any, j:number) => {
          let startDate = moment(this.startDate);
          let date = moment(day.date);
          if(date.isSame(startDate)) {
            this.month[i][j].selected = true;
            this.month[i][j].startSelected = true;
          }
        })
      })
    } else {
      this.month.forEach((week: any, i: number) => {
        week.forEach((day: any, j:number) => {
          delete this.month[i][j].selected;
          delete this.month[i][j].endSelected;
          delete this.month[i][j].startSelected;
        })
      })
    }
  }
}
