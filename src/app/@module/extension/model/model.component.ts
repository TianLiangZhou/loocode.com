import { Component } from '@angular/core';
import {ActivatedRoute} from "@angular/router";
import {BaseComponent} from "../../../@core/base.component";
import {TableSourceService} from "../../../@core/services/table.source.service";
import {MODEL_DELETE, MODELS} from "../../../@core/app.interface.data";
import {AppResponseDataOptions} from "../../../@core/app.data.options";

@Component({
  selector: 'app-model',
  templateUrl: './model.component.html',
  styleUrls: ['./model.component.scss']
})
export class ModelComponent extends BaseComponent {
  settings = {
    actions: {
      position: 'right',
      add: false,
      columnTitle: '操作',
    },
    edit: {
      editButtonContent: '<i class="nb-edit"></i>',
      saveButtonContent: '<i class="nb-checkmark"></i>',
      cancelButtonContent: '<i class="nb-close"></i>',
    },
    delete: {
      deleteButtonContent: '<i class="nb-trash"></i>',
      confirmDelete: true,
    },
    pager: {
      perPage: 30,
    },
    mode: 'external',
    columns: {
      id: {
        title: 'ID',
        type: 'number',
        sort: true,
        filter: true,
      },
      post_title: {
        title: "模型名称",
        type: 'text',
        sort: false,
        filter: false,
      },
      post_status: {
        title: "模型状态",
        type: 'text',
        sort: false,
        filter: false,
      },
      post_modified: {
        title: "修改时间",
        type: 'text',
        sort: false,
        filter: false,
      }
    },
  };

  constructor(route: ActivatedRoute) {
    super(route);
  }

  init() {
    this.serviceSourceConf.next(TableSourceService.getServerSourceConf(MODELS));
  }

  edit($event: any) {
    this.router.navigateByUrl("/app/extension/editing-model/" + $event.getData().id)
  }

  delete($event: any) {
    if (window.confirm("确定删除" + $event.getData().post_title + "模型")) {
      this.http.post(
        MODEL_DELETE.replace("{id}", $event.getData().id), {})
        .subscribe((res: AppResponseDataOptions) => {
          this.toastService.showResponseToast(res.code, this.title, res.message);
          if (res.code == 200) {
            this.source.refresh();
          }
        })
    }
  }

  create() {
    this.router.navigateByUrl("/app/extension/new-model")
  }
}
