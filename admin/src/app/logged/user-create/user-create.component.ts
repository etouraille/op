import { Component, OnInit } from '@angular/core';
import {SubscribeComponent} from "../../../component/subscribe/subscribe.component";
import {AbstractControlOptions, FormBuilder, FormControl, FormGroup, Validators} from "@angular/forms";
import {CustomValidators} from "../../../validator/custom";
import {environment} from "../../../environments/environment";
import {HttpClient} from "@angular/common/http";
import {Router} from "@angular/router";

@Component({
  selector: 'app-user-create',
  templateUrl: './user-create.component.html',
  styleUrls: ['./user-create.component.scss']
})
export class UserCreateComponent extends SubscribeComponent implements OnInit {

  frmSignup : FormGroup;

  constructor(private fb: FormBuilder, private http: HttpClient, private router: Router) {
    super();
    this.frmSignup = this.createSignupForm();
  }


  ngOnInit(): void {
  }

  createSignupForm(): FormGroup {
    return this.fb.group(
      {
        email: [
          null,
          Validators.compose([Validators.email, Validators.required])
        ],
        password: [
          null,
          Validators.compose([
            Validators.required,
            // check whether the entered password has a number
            CustomValidators.patternValidator(/\d/, {
              hasNumber: true
            }),
            // check whether the entered password has upper case letter
            CustomValidators.patternValidator(/[A-Z]/, {
              hasCapitalCase: true
            }),
            // check whether the entered password has a lower case letter
            CustomValidators.patternValidator(/[a-z]/, {
              hasSmallCase: true
            }),
            // check whether the entered password has a special character
            CustomValidators.patternValidator(
              /[ !@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/,
              {
                hasSpecialCharacters: true
              }
            ),
            Validators.minLength(8)
          ])
        ],
        confirmPassword: [null, Validators.compose([Validators.required])],
        roles: [null, Validators.compose([Validators.required])]
      },
      {
        // check whether our password and confirm password match
        validator: CustomValidators.passwordMatchValidator
      } as AbstractControlOptions
    );
  }

  submit() {
    let user = Object.assign({}, this.frmSignup.value);
    const role = user.roles;
    user.roles = [role];
    this.add(
      this.http.post(environment.api + '/api/users', user).subscribe(data => {
        this.router.navigate(['admin/user']);
      })
    )
  }
}
