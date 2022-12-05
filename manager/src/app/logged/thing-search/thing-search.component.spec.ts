import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ThingSearchComponent } from './thing-search.component';

describe('ThingSearchComponent', () => {
  let component: ThingSearchComponent;
  let fixture: ComponentFixture<ThingSearchComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ ThingSearchComponent ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(ThingSearchComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
