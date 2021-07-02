import {ModuleWithProviders, NgModule, Optional, SkipSelf} from '@angular/core';
import { CommonModule } from '@angular/common';
import {NbAuthJWTToken, NbAuthModule, NbPasswordAuthStrategy, NbPasswordAuthStrategyOptions} from '@nebular/auth';
import {NbRoleProvider, NbSecurityModule} from '@nebular/security';

import { of as observableOf } from 'rxjs';
import {StateService} from './services/state.service';
import {AUTHORIZE_LOGIN, AUTHORIZE_LOGOUT, AUTHORIZE_REGISTER} from './app.interface.data';
import {HttpResponse} from "@angular/common/http";


export class NbSimpleRoleProvider extends NbRoleProvider {
    getRole() {
        // here you could provide any role based on any auth flow
        return observableOf('guest');
    }
}

export const NB_CORE_PROVIDERS = [
  StateService,
  ...NbAuthModule.forRoot({
    strategies: [
      NbPasswordAuthStrategy.setup({
        name: 'email',
        token: {
          class: NbAuthJWTToken,
          key: 'data.token'
        },
        baseEndpoint: "",
        login: {
          endpoint: AUTHORIZE_LOGIN,
          method: 'post',
          redirect: {
            success: '/app/dashboard',
            failure: null,
          },
          defaultErrors: ['用户不存在', '用户密码验证失败.'],
          defaultMessages: ['登录成功'],
        },
        register: {
          // ...
          endpoint: AUTHORIZE_REGISTER,
          method: 'post',
          redirect: {
            success: '/app/dashboard',
            failure: null,
          },
          defaultErrors: ['Login/Email combination is not correct, please try again.'],
          defaultMessages: ['You have been successfully logged in.'],
        },
        logout: {
          endpoint: AUTHORIZE_LOGOUT,
          method: 'post',
          redirect: {
            success: '/',
            failure: null,
          },
          defaultErrors: ['Login/Email combination is not correct, please try again.'],
          defaultMessages: ['You have been successfully logged in.'],
        },
        errors: {
          key: 'message',
        },
        messages: {
          key: 'message',
        }
      }),
    ],
    forms: {
    },
  }).providers,
  NbSecurityModule.forRoot({
    accessControl: {
      guest: {
        view: '*',
      },
      user: {
        parent: 'guest',
        create: '*',
        edit: '*',
        remove: '*',
      },
    },
  }).providers,
  {
    provide: NbRoleProvider, useClass: NbSimpleRoleProvider,
  },
];

function throwIfAlreadyLoaded(parentModule: any, moduleName: string) {
  if (parentModule) {
    throw new Error(`${moduleName} has already been loaded. Import Core modules in the AppModule only.`);
  }
}

@NgModule({
  declarations: [],
  imports: [
    CommonModule
  ],
  exports: [
    NbAuthModule
  ]
})
export class CoreModule {
  constructor(@Optional() @SkipSelf() parentModule: CoreModule) {
    throwIfAlreadyLoaded(parentModule, 'CoreModule');
  }

  static forRoot(): ModuleWithProviders<CoreModule> {
    return {
      ngModule: CoreModule,
      providers: [
        ...NB_CORE_PROVIDERS,
      ],
    };
  }
}
