import { Injectable } from '@angular/core';

@Injectable({
  providedIn: 'root'
})
export class StorageService {

  constructor() { }

  get(key: string ): string|object|null {
    let value = window.localStorage.getItem(key);
    try {
      value = value!== null ? JSON.parse(value): null;
    } catch(error) {}
    return value;
  }

  set(key: string, value: any): void {
    let stringValue: string;
    try {
      stringValue = JSON.stringify(value);
    } catch(err) {

    }
    // @ts-ignore
    if(typeof stringValue =='undefined') {
      stringValue = value;
    }
    window.localStorage.setItem(key, stringValue);
  }
}
