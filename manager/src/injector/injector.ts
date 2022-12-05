import {Injectable} from "@angular/core";
import {HttpHandler, HttpInterceptor, HttpRequest, HttpResponse} from "@angular/common/http";
import {finalize, tap} from "rxjs";
import {environment} from "../environments/environment";
import {StorageService} from "../service/storage.service";

@Injectable()
export class AuthInterceptor implements HttpInterceptor {

  constructor(private service : StorageService) {}

  intercept(req: HttpRequest<any>, next: HttpHandler) {
    // Get the auth token from the service.
    const authToken = 'Bearer ' + this.service.get('token');
    let headers;
    if( req.method === 'PATCH') {
      headers = req.headers.set('Authorization', authToken).set('content-type', 'application/merge-patch+json');
    } else {
      headers = req.headers.set('Authorization', authToken);
    }
    // Clone the request and repla
    // ce the original headers with
    // cloned headers, updated with the authorization.
    const authReq = req.clone({
      headers: headers,
      url: req.url.match(/\/upload/)? req.url : (environment.api + req.url)
    });

    let ok : string;
    const started = Date.now();

    // send cloned request with header to the next handler.
    return next.handle(authReq)
      .pipe(
        tap({
          // Succeeds when there is a response; ignore other events
          next: (event) => (ok = event instanceof HttpResponse ? 'succeeded' : ''),
          // Operation failed; error is an HttpErrorResponse
          error: (error) => { ok = 'failed'; console.log(error) }
        }),
        // Log when response observable either completes or errors
        finalize(() => {
          const elapsed = Date.now() - started;
          const msg = `${req.method} "${req.urlWithParams}"
             ${ok} in ${elapsed} ms.`;
          console.log(msg);
        })
      )
      ;
  }
}
