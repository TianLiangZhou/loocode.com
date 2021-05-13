import {
  Component,
} from '@angular/core';
import {DASHBOARD} from '../../@core/app.interface.data';
import {AppResponseDataOptions} from '../../@core/app.data.options';
import {BaseComponent} from '../../@core/base.component';
import {ActivatedRoute} from "@angular/router";

@Component({
  selector: 'app-dashboard',
  templateUrl: './dashboard.component.html',
  styleUrls: ['./dashboard.component.scss']
})
export class DashboardComponent extends BaseComponent {
  card: {[key:string]:any}[] = [];
  constructor(private readonly r: ActivatedRoute,) {
    super(r);
  }
  init() {
    this.http.request('get', DASHBOARD, {})
      .subscribe((res: AppResponseDataOptions) => {
        if (res.code === 200) {
          this.card = res.data;
        }
      });
  }
}
