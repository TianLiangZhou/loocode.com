import {Component, ViewChild} from '@angular/core';
import {BaseComponent} from "../../../@core/base.component";
import {ActivatedRoute} from "@angular/router";
import {AppResponseDataOptions} from "../../../@core/app.data.options";
import {MetaComponent} from "../../../@theme/components/meta/meta.component";
import {TableSourceService} from "../../../@core/services/table.source.service";

@Component({
  selector: 'app-dynamic-model',
  templateUrl: './dynamic-model.component.html',
  styleUrls: ['./dynamic-model.component.scss']
})
export class DynamicModelComponent extends BaseComponent {

  @ViewChild("metaComponent", {static: true}) metaComponent: MetaComponent;

  metas = [];

  isList = false;
  settings: any;
  isTaxonomy: boolean = false;
  data: {[key: string]: any} = {};

  metaBindValue = {};

  private id = 0;



  constructor(route: ActivatedRoute) {
    super(route);
  }

  init() {
    this.route.params.subscribe(data => {
      if (!data.id) {
        return ;
      }
      this.http.get("/model/" + data.id).subscribe((res: AppResponseDataOptions) => {
        if (res.code != 200) {
          return false;
        }
        this.id = data.id;
        this.metas = res.data.option.meta.forms;
        this.title = res.data.name;
        this.isList = res.data.option.template != 3;
        this.isTaxonomy = res.data.option.template == 2;
        this.isList && this.buildSetting();
        if (this.isList) {
          this.serviceSourceConf.next(TableSourceService.getServerSourceConf(
            "/model/" + data.id + "/data"
          ));
        } else {
          this.http.get("/model/" + data.id + "/data").subscribe((res: AppResponseDataOptions) => {
            if (res.code == 200) {
              this.data = this.metaBindValue = res.data;
            }
          });
        }
      });
    })
  }

  action($event: any) {
    this.submitted = true;
    if (this.isTaxonomy) {
      this.data.meta = this.metaComponent.metaBindModel;
    } else {
      this.data = Object.assign(this.data, this.metaComponent.metaBindModel);
    }
    this.http.post("/model/" + this.id + "/data", this.data).subscribe((res: AppResponseDataOptions) => {
      this.toastService.showResponseToast(res.code, this.title, res.message);
      this.submitted = false;
      if (res.code == 200) {
        if (res.data.id > 0) {
          this.data.id = res.data.id;
          this.currentMode = 'editor';
        }
        if (this.isList) {
          this.source.refresh();
        }
      }
    }, () => this.submitted = false);
  }

  edit($event: any) {
    this.currentMode = 'editor';
    if (this.isTaxonomy) {
      this.data = {
        id: $event.data.term_taxonomy_id,
        description: $event.data.description,
        name: $event.data.term.name,
        slug: $event.data.term.slug,
      };
      if ($event.data.meta.length > 0) {
        let meta = {};
        $event.data.meta.forEach((item, index) => {
          meta[item.meta_key] = item.value;
        });
        this.metaBindValue = meta;
      }
    } else {
      this.data = $event.data;
      this.metaBindValue = $event.data;
    }
  }

  delete($event: any) {
    if (window.confirm("确定删除[" + $event.getData().id + "]记录")) {
      this.http.post("/model/" + this.id + "/data/" + $event.getData().id + "/delete", {})
        .subscribe((res: AppResponseDataOptions) => {
          this.toastService.showResponseToast(res.code, this.title, res.message);
          if (res.code == 200) {
            this.source.refresh();
          }
        });
    }
  }

  private buildSetting() {
    let settings = {
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
      columns: {}
    };
    let columns: {[key: string]: any} = {
      id: {
        title: "ID",
        type: 'number',
        sort: true,
        filter: true,
      }
    };
    this.metas.forEach((item, index) => {
      columns[item.keyword] = {
        title: item.name,
        type: 'string',
        sort: false,
        filter: false,
      };
    });
    if (this.isTaxonomy) {
      columns = {
        name: {
          title: '名称',
          type: 'string',
          sort: false,
          filter: true,
          valuePrepareFunction: (avatar: string, row: any) => {
            return row.term.name;
          },
        },
        description: {
          title: '内容描述',
          type: 'string',
          sort: false,
          filter: false,
        },
        slug: {
          title: '别名',
          type: 'string',
          sort: false,
          filter: false,
          valuePrepareFunction: (avatar: string, row: any) => {
            return row.term.slug;
          },
        },
        count: {
          title: '总数',
          type: 'string',
          sort: true,
          filter: false,
        }
      };
    }
    settings.columns = columns;
    this.settings = settings;
  }
}
