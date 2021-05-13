import { Component, OnInit } from '@angular/core';
import {BaseComponent} from "../../@core/base.component";
import {TableSourceService} from "../../@core/services/table.source.service";
import {PAGES, POSTS} from "../../@core/app.interface.data";
import {Row} from "ng2-smart-table/lib/lib/data-set/row";

@Component({
  selector: 'app-page',
  template: `
    <nb-card>
      <nb-card-header>
        {{ title }}
        <div class="float-right">
          <button size="small" status="success" (click)="create(null)" nbButton>
            新建页面<nb-icon icon="plus"></nb-icon>
          </button>
        </div>
      </nb-card-header>

      <nb-card-body>
        <ng2-smart-table
          [settings]="settings"
          [source]="source"
          (edit)="edit($event)"
          (delete)="delete($event)"
        >
        </ng2-smart-table>
      </nb-card-body>
    </nb-card>
  `,
  styles: [],
})
export class PageComponent extends BaseComponent {
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
      id: {
        title: 'ID',
        type: 'number',
        sort: true,
        filter: true,
      },
      post_author: {
        title: '作者',
        type: 'string',
        sort: false,
        filter: true,
      },
      post_title: {
        title: '标题',
        type: 'string',
        sort: false,
        filter: true,
      },
      post_status: {
        title: '状态',
        type: 'string',
        sort: false,
        filter: true,
      },
      post_modified: {
        title: '时间',
        type: 'string',
        sort: false,
        filter: false,
      }
    },
  }

  init() {
    this.serviceSourceConf.next(TableSourceService.getServerSourceConf(PAGES));
  }

  create($event: any) {
    this.router.navigateByUrl("/app/page/new");
  }

  edit($event: Row) {
    this.router.navigateByUrl("/app/page/editing/"+$event.getData().id);
  }

  delete($event: any) {

  }
}
