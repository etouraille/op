import { Injectable } from '@angular/core';
import { ActivatedRouteSnapshot, CanActivate, RouterStateSnapshot, UrlTree } from '@angular/router';
import {map, Observable} from 'rxjs';
import {HttpClient} from "@angular/common/http";
import jwt_decode from 'jwt-decode';
import {StorageService} from "../service/storage.service";
@Injectable({
  providedIn: 'root'
})
export class IsAdminGuard implements CanActivate {

  constructor(private http: HttpClient, private service : StorageService) {

  }

  canActivate(
    route: ActivatedRouteSnapshot,
    state: RouterStateSnapshot):
    Observable<boolean | UrlTree>
    | Promise<boolean | UrlTree>
    | boolean | UrlTree {
        let token = <any>jwt_decode(<string>this.service.get('token'));
        if(token) {
          return token.roles.includes('ROLE_ADMIN');
        }
        return false;
    }

}
