import {Component, ViewChild} from '@angular/core';
import {TableSourceService} from '../../../@core/services/table.source.service';
import {USER_CREATE_MEMBER, USER_MEMBER, USER_UPDATE_MEMBER} from '../../../@core/app.interface.data';
import {Row} from 'ng2-smart-table/lib/lib/data-set/row';
import {AppResponseDataOptions} from '../../../@core/app.data.options';
import {BaseComponent} from '../../../@core/base.component';
import {CKFinderService} from "../../../@core/services/ckfinder.service";

@Component({
  selector: 'app-user-member',
  templateUrl: './user.member.component.html',
  styleUrls: ['./user.member.component.scss']
})
export class UserMemberComponent  extends BaseComponent {

  settings = {};

  @ViewChild('storeWindow', {static: false}) storeWindow;

  user = {
    id: 0,
    name: '',
    email: '',
    password: '',
    avatar: '',
  };
  ckfinder: CKFinderService;

  init() {
    this.settings = UserMemberComponent.smartTableSettings();
    this.serviceSourceConf.next(TableSourceService.getServerSourceConf(USER_MEMBER));
    this.ckfinder = this.injector.get<CKFinderService>(CKFinderService)
    this.ckfinder.subscribe((item: any) => {
      this.user.avatar =  item.url;
    });
  }

  onCloseDialogCallback() {
    this.user = {
      id: 0,
      name: '',
      email: '',
      password: '',
      avatar: '',
    };
  }

  create() {
    this.popupOperationDialog(
      'create',
    );
  }

  edit(data: Row) {
    this.user = data.getData();
    this.popupOperationDialog(
      'editor',
    );
  }

  action() {
    this.submitted = true;
    if (this.user.name.replace(/\s+/g, '') == '') {
      this.submitted = false;
      return this.failureToast('昵称不能为空!');
    }
    const re = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    if (!re.test(String(this.user.email).toLowerCase())) {
      return this.failureToast('邮箱格式错误!');
    }
    if (this.currentMode === 'create' && this.user.password.replace(/\s+/g, '') == '') {
      this.submitted = false;
      return this.failureToast('密码不能为空!');
    }
    const data = {...this.user};
    this.http.request(
      'post',
      this.currentMode === 'create'
        ? USER_CREATE_MEMBER
        : USER_UPDATE_MEMBER.replace('{id}', this.user.id.toString())
      ,
      {body: data}
      )
      .subscribe((res: AppResponseDataOptions) => {
        this.toastService.showResponseToast(res.code, this.operationSubject(), res.message);
        this.submitted = false;
        if (res.code !== 200) {
          return ;
        }
        this.nbWindowRef.close();
        if (this.user.id > 0) {
          this.source.refresh();
        } else {
          this.source.append(res.data);
        }
      });
    return true;
  }

  private static smartTableSettings() {
    return {
      actions: {
        position: 'right',
        edit: true,
        delete: false,
        create: true,
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
          sort: true,
          filter: true,
        },
        user_nicename: {
          title: '用户昵称',
          type: 'string',
          sort: false,
          filter: true,
        },
        email: {
          title: '邮箱',
          type: 'string',
          sort: false,
          filter: true,
        },
        avatar: {
          title: '头像',
          type: 'html',
          valuePrepareFunction: (avatar: string) => `<img width="50" alt="" src="${avatar}" />`,
          filter: false,
          sort: false,
        },
        created_at: {
          title: '加入时间',
          type: 'string',
          sort: false,
          filter: false,
        }
      },
    };
  }
}
