import { Injectable } from '@angular/core';
import {ActivatedRouteSnapshot, CanActivate, Router, RouterStateSnapshot, UrlTree} from '@angular/router';
import {catchError, map, Observable, of, tap} from 'rxjs';
import {HttpClient} from "@angular/common/http";
import jwt_decode from 'jwt-decode';
import {StorageService} from "../service/storage.service";
@Injectable({
  providedIn: 'root'
})
export class IsAdminGuard implements CanActivate {

  constructor(
    private http: HttpClient,
    private service : StorageService,
    private router: Router,
  ) {

  }

  canActivate(
    route: ActivatedRouteSnapshot,
    state: RouterStateSnapshot):
    Observable<boolean | UrlTree>
    | Promise<boolean | UrlTree>
    | boolean | UrlTree {
        return this
          .http
          .get('api/ping')
          .pipe(
            catchError(error=> of(false)),
            map((data:any ) => {
              return (data.roles ? data.roles.includes('ROLE_ADMIN'): false)
            }),
            tap((ret) => {
              ret === false ? this.router.navigate(['login']) : null;
            })
          )
      ;
    }
}
