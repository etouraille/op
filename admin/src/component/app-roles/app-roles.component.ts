import {Component, forwardRef, OnChanges, OnInit, SimpleChanges} from '@angular/core';
import {SubscribeComponent} from "../subscribe/subscribe.component";
import {ControlValueAccessor, NG_VALUE_ACCESSOR} from "@angular/forms";

@Component({
  selector: 'app-roles',
  templateUrl: './app-roles.component.html',
  styleUrls: ['./app-roles.component.scss'],
  providers: [{
    provide: NG_VALUE_ACCESSOR,
    useExisting: forwardRef(() => AppRolesComponent),
    multi: true
  }]
})
export class AppRolesComponent extends SubscribeComponent implements OnInit, ControlValueAccessor , OnChanges {
  onChange: any  = () => {};
  onTouch: any = () => {}

  _roles: string[] = [];

  show = false;

  user: boolean = false;
  admin: boolean = false;

  toggle(event: any) {
    this.show = !this.show;
  }

  constructor() {
    super();
  }

  ngOnInit(): void {
  }

  ngOnChanges(changes: SimpleChanges) {
    console.log(changes);
  }

  writeValue(roles: string[]) {
    this._roles = roles;
    if(roles.includes('ROLE_USER')) {
      this.user = true;
    }
    if(roles.includes('ROLE_ADMIN')) {
      this.admin = true;
    }
  }

  registerOnChange(fn: any) {
    this.onChange = fn;
  }

  registerOnTouched(fn: any) {
    this.onTouch = fn;
  }

  changeUser() {
    if(this.user) {
      this._roles.push('ROLE_USER')
    } else {
      let index = this._roles.indexOf('ROLE_USER');
      if( index > -1) {
        this._roles.splice(index, 1);
      }
      this.onChange(this._roles);
    }
  }

  changeAdmin() {
    if(this.admin) {
      this._roles.push('ROLE_ADMIN')
    } else {
      let index = this._roles.indexOf('ROLE_ADMIN');
      if( index > -1) {
        this._roles.splice(index, 1);
      }
      this.onChange(this._roles);
    }
  }

}
