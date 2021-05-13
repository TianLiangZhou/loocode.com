import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { UserRoutingModule } from './user-routing.module';
import {UserMemberComponent} from './member/user.member.component';
import {ThemeModule} from '../../@theme/theme.module';
import {UserComponent} from './user.component';
import {Ng2SmartTableModule} from 'ng2-smart-table';
import {NbWindowService} from '@nebular/theme';
import { ProfileComponent } from './profile/profile.component';
import {CKFinderService} from "../../@core/services/ckfinder.service";

@NgModule({
  declarations: [
    UserComponent,
    UserMemberComponent,
    ProfileComponent,
  ],
  imports: [
    CommonModule,
    ThemeModule,
    UserRoutingModule,
    Ng2SmartTableModule,
  ],
  providers: [
    NbWindowService,
    CKFinderService,
  ]
})
export class UserModule { }
