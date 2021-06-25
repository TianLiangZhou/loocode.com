import { Component } from '@angular/core';
import {BaseComponent} from "../../../@core/base.component";
import {ActivatedRoute} from "@angular/router";
import {AppResponseDataOptions} from "../../../@core/app.data.options";
import {MODEL_STORE, MODEL_UPDATE} from "../../../@core/app.interface.data";

@Component({
  selector: 'app-new-model',
  templateUrl: './new-model.component.html',
  styleUrls: ['./new-model.component.scss']
})
export class NewModelComponent extends BaseComponent {

  model: {[key: string]: any} = {
    id: 0,
    name: "",
    identity: "",
    option: {
      template: 1,
      menu_level: 1,
      meta: {
        forms: [],
      },
      save_type: 'config',
      taxonomy: '',
      parent_menu_id: 0,
      parent_menu_name: "",
      menu_name: "",
    }
  };
  formType = [];
  taxonomies: any[];
  constructor(route: ActivatedRoute) {
    super(route);
  }
  menus = [];

  init() {
    this.taxonomies = this.appConfig.taxonomy;
    this.http.get('/model/top/menu').subscribe((res: AppResponseDataOptions) => {
      this.menus = res.data;
    });
    this.route.params.subscribe(data => {
      if (data.id) {
        this.http.get("/model/" + data.id).subscribe((res: AppResponseDataOptions) => {
            if (res.code == 200) {
                this.model = res.data;
            }
        });
      }
    })
  }

  save() {
    if (this.model.name.trim() == "") {
      return this.failureToast("模型名称不能为空");
    }
    this.submitted = true;
    let url = MODEL_STORE;
    if (this.model.id > 0) {
      url = MODEL_UPDATE.replace("{id}", this.model.id);
    }
    if (this.model.option.template == 2 && this.model.option.taxonomy == "") {
      return this.failureToast("请选择归类标识");
    }
    this.http.post(url, this.model).subscribe((res: AppResponseDataOptions) => {
      if (res.code == 200) {
        this.model.id = res.data.id;
      }
      this.toastService.showResponseToast(res.code, this.title, res.message);
      this.submitted = false;
    }, () => {this.submitted = false});
  }

  menuChange($event: any) {
    switch ($event) {
      case 1:
        this.model.option.parent_menu_id = 0;
        this.model.option.parent_menu_name = "";
        break;
      case 3:
        this.model.option.parent_menu_name = "";
        break;
    }
  }

  onChangeTemplate($event: any) {
    switch ($event) {
      case 1:
        // this.model.option.save_type = 'config';
        break;
      case 2:
        this.model.identity  = "";
        // this.model.option.save_type = 'row';
        break;
      case 3:
        this.model.identity  = "";
        // this.model.option.save_type = 'config';
        break;
    }
  }
}
