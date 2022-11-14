import { ComponentFixture, TestBed } from '@angular/core/testing';

import { AppRolesComponent } from './app-roles.component';

describe('AppRolesComponent', () => {
  let component: AppRolesComponent;
  let fixture: ComponentFixture<AppRolesComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ AppRolesComponent ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(AppRolesComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
