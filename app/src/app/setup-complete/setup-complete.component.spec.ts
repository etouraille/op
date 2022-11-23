import { ComponentFixture, TestBed } from '@angular/core/testing';

import { SetupCompleteComponent } from './setup-complete.component';

describe('SetupCompleteComponent', () => {
  let component: SetupCompleteComponent;
  let fixture: ComponentFixture<SetupCompleteComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ SetupCompleteComponent ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(SetupCompleteComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
