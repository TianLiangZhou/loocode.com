import {Component, ViewChild} from '@angular/core';
import {TableSourceService} from "../../../@core/services/table.source.service";
import {
  CATEGORIES,
  CATEGORY_DELETE,
  CATEGORY_STORE,
  CATEGORY_UPDATE,
  EXTENSION_META_TYPE,
} from "../../../@core/app.interface.data";
import {BaseComponent} from "../../../@core/base.component";
import {AppResponseDataOptions} from "../../../@core/app.data.options";
import {MetaComponent} from "../../../@theme/components/meta/meta.component";

@Component({
  selector: 'app-category',
  templateUrl: './category.component.html',
  styleUrls: ['./category.component.scss']
})
export class CategoryComponent extends BaseComponent {
  @ViewChild("metaComponent", {static: false}) metaComponent: MetaComponent;
  settings = {
    actions: {
      position: 'right',
      add: false,
      columnTitle: '操作',
    },
    add: {
      addButtonContent: '<i class="nb-plus"></i>',
      createButtonContent: '<i class="nb-checkmark"></i>',
      cancelButtonContent: '<i class="nb-close"></i>',
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
    },
  };
  category: {[key: string]: any} = {
    id: 0,
    name: "",
    slug: "",
    parent: 0,
    description: "",
  };
  categories: any[] = [];
  metas = [];

  init() {
    this.serviceSourceConf.next(TableSourceService.getServerSourceConf(CATEGORIES));
    this.source.rawData.subscribe((res) => {
      if (res.code == 200) {
        this.categories = res.data;
      }
    });
    this.http.get(EXTENSION_META_TYPE + "/category").subscribe((res:AppResponseDataOptions) => {
      if (res.code !== 200) {
        return;
      }
      this.metas = res.data.forms;
    });
  }

  action($event: any) {
    if (this.category.name.trim() == "") {
      return this.failureToast("名称不能为空");
    }
    this.submitted = true;
    this.category.taxonomy = "category";
    this.category.metas = this.metaComponent.metaBindModel;
    let url = CATEGORY_STORE
    if (this.category.id > 0) {
      url = CATEGORY_UPDATE.replace("{id}", this.category.id.toString())
    }
    this.http.post(url, this.category).subscribe((res:AppResponseDataOptions) => {
      this.toastService.showResponseToast(res.code, this.title, res.message);
      this.submitted = false;
      if (res.code == 200) {
        this.source.refresh();
      }
    }, (error) => {
      this.submitted = !this.submitted;
    });
  }

  edit($event: any) {
    this.currentMode = 'editor';
    const data = $event.data;
    this.category = {
      id: data.term_taxonomy_id,
      name: data.term.name.replaceAll("— ", "").trim(),
      slug: data.term.slug,
      description: data.description,
      parent: data.parent,
    };
  }

  delete($event: any) {
    const data = $event.data;
    if (window.confirm("确定删除" + this.title + ": " + data.term.name.replaceAll("— ", "").trim())) {
      this.http.delete(CATEGORY_DELETE.replace("{id}", data.term_taxonomy_id)).subscribe((res:AppResponseDataOptions) => {
        this.toastService.showResponseToast(res.code, this.title, res.message);
        if (res.code == 200) {
          this.source.refresh();
        }
      });
    }
  }

}
