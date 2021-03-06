import {Component, Input, OnInit, TemplateRef, ViewChild} from '@angular/core';
import {TableSourceService} from '../../../@core/services/table.source.service';
import {MANAGERS, MANAGER_DELETE, MANAGER_STORE, ROLES, MANAGER_UPDATE} from '../../../@core/app.interface.data';
import {Row} from 'ng2-smart-table/lib/lib/data-set/row';
import {AppResponseDataOptions, Manager} from '../../../@core/app.data.options';
import {BaseComponent} from '../../../@core/base.component';

@Component({
  selector: 'app-user-manager',
  templateUrl: './system.manager.component.html',
  styleUrls: ['./system.manager.component.scss']
})
export class SystemManagerComponent extends BaseComponent {
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
      ID: {
        title: 'ID',
        type: 'number',
      },
      email: {
        title: '邮箱',
        type: 'string',
        sort: false,
        width: "20%"
      },
      avatar: {
        title: '头像',
        type: 'html',
        valuePrepareFunction: (avatar: string) => {
            return `<img src="${avatar}" width="100px" />`;
        },
        filter: false,
        sort: false,
      },
      role_name: {
        title: '角色',
        type: 'string',
        filter: false,
        sort: false,
      },
      lasted_date: {
        title: '最后登录时间',
        type: 'string',
        sort: false,
        filter: false,
      }
    },
  };

  @Input() manager: Manager = {
    ID: 0,
    user_login: '',
    email: '',
    password: '',
    avatar: '',
    roles: [],
  };

  roles = [];

  delete($event: Row) {
    if (confirm('确定删除---' + $event.getData().email)) {
      this.http.delete(MANAGER_DELETE.replace('{id}', $event.getData().ID))
          .subscribe((res: AppResponseDataOptions) => {
            this.toastService.showResponseToast(res.code, this.title, res.message);
            if (res.code === 200) {
              this.source.refresh();
            }
          });
      return true;
    }
  }

  edit($event: Row) {
    this.currentMode = 'editor';
    this.manager = $event.getData();
  }

  action($event: any) {
    if (this.manager.user_login.trim() === '') {
      return this.failureToast('昵称不能为空');
    }
    if (this.manager.email.trim() === '') {
      return this.failureToast('邮箱不能为空');
    }
    if (this.currentMode === 'create' && this.manager.password.trim() === '') {
      return this.failureToast('密码不能为空');
    }

    if (this.manager.password && this.manager.password.length < 6) {
      return this.failureToast('密码不能小于6位数');
    }
    this.submitted = true;
    let url = MANAGER_STORE;
    if (this.manager.ID > 0) {
      url = MANAGER_UPDATE.replace('{id}', this.manager.ID.toString());
    }
    this.http.request('post', url, {body: this.manager})
      .subscribe((res: AppResponseDataOptions) => {
        this.submitted = false;
        this.toastService.showResponseToast(res.code, this.operationSubject(), res.message);
        if (res.code !== 200) {
          return ;
        }
        this.source.refresh();
      });
    return true;
  }

  init() {
    this.serviceSourceConf.next(TableSourceService.getServerSourceConf(MANAGERS));
    this.http.get(ROLES+'?data_per_page=1024').subscribe((res: AppResponseDataOptions) => {
      if (res.code === 200) {
        this.roles = res.data.data;
      }
    });
  }
}
